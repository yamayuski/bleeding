<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

use Bleeding\Console\Attributes\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

return
#[Command('hello')]
fn (InputInterface $input, OutputInterface $output) => $output->writeln('Hello world');
