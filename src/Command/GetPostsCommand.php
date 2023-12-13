<?php
// src/Command/CreateUserCommand.php
namespace App\Command;

use App\Service\PostsService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'posts:get')]
class GetPostsCommand extends Command
{
  public function __construct(
    private PostsService $postsService
  )
  {
    parent::__construct();
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $users = $this->postsService->getExternalUsers();
    if (!$users) {
      Command::FAILURE;
    }


    $res = $this->postsService->storeExternalUsers($users);
    if (!$res) {
      Command::FAILURE;
    }

    $posts = $this->postsService->getExternalPosts();
    if (!$posts) {
      Command::FAILURE;
    }

    $res = $this->postsService->storeExternalPosts($posts);
    if (!$res) {
      return Command::FAILURE;
    }

    return Command::SUCCESS;
  }
}