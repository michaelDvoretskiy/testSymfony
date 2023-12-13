<?php

namespace App\Controller;

use App\Entity\Post;
use App\Service\PostsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

  #[Route('/', name: 'app_root')]
  public function redirectToLista(): RedirectResponse
  {
    return $this->redirectToRoute('app_lista');
  }

  #[Route('/lista', name: 'app_lista')]
  public function getPosts(ParameterBagInterface $params): Response
  {
    $subdir = $params->get('api.sub-dir');
    $posts = $this->postsService->getPosts();
    return $this->render('posts.html.twig', ['posts' => $posts, 'subdir' => $subdir]);
  }

  #[Route('/posts', methods: ['GET'], name: 'app_posts')]
  public function getPostsApi(SerializerInterface $serializer): JsonResponse
  {
    $posts = $this->postsService->getPosts();
    $jsonContent = $serializer->serialize($posts, 'json', [
      'groups' => ['posts', 'users'],
    ]);
    return new JsonResponse($jsonContent, 200, [], true);
  }

  #[Route('/edit-posts/{sourceId}', methods: ['GET'], name: 'app_delete_post', requirements: ['sourceId' => '\d+'])]
  public function deletePost(int $sourceId): JsonResponse
  {
    $res = $this->postsService->removePost($sourceId);
    return $this->json(['success' => $res]);
  }

  #[Route('/edit-posts/clear', methods: ['GET'], name: 'app_clear_posts')]
  public function clearPosts(): RedirectResponse
  {
    $res = $this->postsService->clearPosts();
    return $this->redirectToLista();
  }

  #[Route('/edit-posts/download', methods: ['GET'], name: 'app_download_posts')]
  public function downloadPosts(): RedirectResponse
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
    
    return $this->redirectToLista();
  }
}
