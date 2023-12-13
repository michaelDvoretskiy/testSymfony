<?php

namespace App\Controller;

use App\Entity\Post;
use App\Service\PostsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PostController extends AbstractController
{
  public function __construct(
    private PostsService $postsService
  )
  {
  }

  #[Route('/lista', name: 'app_lista')]
  public function getPosts(): Response
  {
    $posts = $this->postsService->getPosts();
    return $this->render('posts.html.twig', ['posts' => $posts]);
  }

  #[Route('/posts', name: 'app_posts')]
  public function getPostsApi(SerializerInterface $serializer): JsonResponse
  {
    $posts = $this->postsService->getPosts();
    $jsonContent = $serializer->serialize($posts, 'json', [
      'groups' => ['posts', 'users'],
    ]);
    return new JsonResponse($jsonContent, 200, [], true);
  }

  #[Route('/posts/{sourceId}', methods: ['DELETE'], name: 'app_delete_post', requirements: ['sourceId' => '\d+'])]
  public function deletePost(int $sourceId): JsonResponse
  {
    $res = $this->postsService->removePost($sourceId);
    return $this->json(['success' => $res]);
  }

  #[Route('/posts/clear', methods: ['DELETE'], name: 'app_clear_posts')]
  public function clearPosts(): JsonResponse
  {
    $res = $this->postsService->clearPosts();
    return $this->json(['success' => $res]);
  }

  #[Route('/posts/download', methods: ['POST'], name: 'app_download_posts')]
  public function downloadPosts(): JsonResponse
  {
    $users = $this->postsService->getExternalUsers();
    if (!$users) {
      return $this->json(['success' => false]);
    }

    $res = $this->postsService->storeExternalUsers($users);
    if (!$res) {
      return $this->json(['success' => false]);
    }

    $posts = $this->postsService->getExternalPosts();
    if (!$posts) {
      return $this->json(['success' => false]);
    }

    $res = $this->postsService->storeExternalPosts($posts);
    if (!$res) {
      return $this->json(['success' => false]);
    }

    return $this->json(['success' => true]);
  }
}