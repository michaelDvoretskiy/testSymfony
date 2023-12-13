<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\PostUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

class PostsService
{
  private $baseUrl;
  private $recorndsToFlush;

  public function __construct(
    private HttpClientInterface $client,
    private ParameterBagInterface $params,
    private EntityManagerInterface $entityManager
  )
  {
    $this->baseUrl = $this->params->get('api.url');
    $this->recorndsToFlush = $this->params->get('api.records-flush');
  }

  public function clearPosts()
  {
    try {
      $this->entityManager->getRepository(Post::class)
      ->clearPosts();
    $this->entityManager->getRepository(PostUser::class)
      ->clearUsers();

    } catch (Throwable $e) {
      return false;  
    }
    return true;
  }

  public function removePost(int $sourceId)
  {
    $post = $this->entityManager->getRepository(Post::class)
      ->findOneBy(['sourceId' => $sourceId]);
    if (!$post) {
      return false;
    }
    $this->entityManager->remove($post);
    $this->entityManager->flush();

    return true;
  }

  public function getPosts()
  {
    return $this->entityManager->getRepository(Post::class)
      ->findBy([],['title' => 'asc']);
  }

  public function getExternalPosts()
  {
    $url = $this->baseUrl . $this->params->get('api.posts');
    //return $this->getExternalData($url);
    return $this->getInternalData('posts');
  }

  public function getExternalUsers()
  {
    $url = $this->baseUrl . $this->params->get('api.users');
    //return $this->getExternalData($url);
    return $this->getInternalData('users');
  }

  public function storeExternalPosts(array $posts): bool
  {
    $recordsProcessed = 0;
    try {
      foreach ($posts as $postData) {
        $postUser = $this->entityManager->getRepository(PostUser::class)
          ->findOneBy(['sourceId' => $postData['userId']]);
        if(!$postUser) {
          continue;
        }
        $post = $this->entityManager->getRepository(Post::class)
          ->findOneBy(['sourceId' => $postData['id']]);
        if (!$post) {
          $post = (new Post())->setSourceId($postData['id']);
          $this->entityManager->persist($post);
        }
        $post
          ->setPostUser($postUser)
          ->setTitle($postData['title'])
          ->setBody($postData['body']);
        $recordsProcessed++;
        if($recordsProcessed >= $this->recorndsToFlush) {
          $this->entityManager->flush();
          $recordsProcessed = 0;
        }
      }
      if ($recordsProcessed > 0) {
        $this->entityManager->flush();
      }
    } catch(\Throwable $exception) {
      return false;
    }

    return true;
  }

  public function storeExternalUsers(array $users): bool
  {
    $recordsProcessed = 0;
    try {
      foreach ($users as $userData) {
        $postUser = $this->entityManager->getRepository(PostUser::class)
          ->findOneBy(['sourceId' => $userData['id']]);
        if (!$postUser) {
          $postUser = (new PostUser())->setSourceId($userData['id']);
          $this->entityManager->persist($postUser);
        }
        $postUser
          ->setName($userData['name']);
        $recordsProcessed++;
        if($recordsProcessed >= $this->recorndsToFlush) {
          $this->entityManager->flush();
          $recordsProcessed = 0;
        }
      }
      if ($recordsProcessed > 0) {
        $this->entityManager->flush();
      }
    } catch(\Throwable $exception) {
      return false;
    }

    return true;
  }

  private function getExternalData($url): array
  {
    $response = $this->client->request('GET', $url, [
      'headers' => [
        'Accept' => 'application/json',
      ],
    ]);

    $statusCode = $response->getStatusCode();
    if ($statusCode == 200) {
      try {
        $content = $response->toArray();
        return $content;
      } catch(\Throwable $exception) {
        return false;
      }
    }

    return false;
  }

  private function getInternalData($name): array
  {
    $jsonStr = file_get_contents(__DIR__ . "/../../".$name.".json");
    return json_decode($jsonStr, true);
  }
}