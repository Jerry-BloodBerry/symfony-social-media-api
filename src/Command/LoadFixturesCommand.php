<?php

namespace App\Command;

use App\Fixture\FixtureFactory;
use App\Fixture\PostFixture;
use App\Fixture\UserFixture;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
  name: 'app:load-fixtures',
  description: 'Load database fixtures',
  hidden: false
)]
class LoadFixturesCommand extends Command
{
  private FixtureFactory $fixtureFactory;

  public function __construct(FixtureFactory $fixtureFactory)
  {
    $this->fixtureFactory = $fixtureFactory;

    parent::__construct();
  }

  protected function initialize(InputInterface $input, OutputInterface $output): void
  {
    $this->fixtureFactory->registerFixture(UserFixture::class, 200);
    $this->fixtureFactory->registerFixture(PostFixture::class, 100);
    // Register more fixtures as needed
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $this->fixtureFactory->load();
    $output->writeln('Fixtures loaded successfully.');

    return Command::SUCCESS;
  }
}
