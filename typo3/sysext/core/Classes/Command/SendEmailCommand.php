<?php
namespace TYPO3\CMS\Core\Command;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Mail\Mailer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Command for sending spooled messages.
 *
 * Inspired and partially taken from symfony's swiftmailer package.
 *
 * @link https://github.com/symfony/swiftmailer-bundle/blob/master/Command/SendEmailCommand.php
 */
class SendEmailCommand extends Command
{
    protected function configure()
    {
        $this
            ->setDescription('Sends emails from the spool')
            ->addOption('message-limit', null, InputArgument::REQUIRED, 'The maximum number of messages to send.')
            ->addOption('time-limit', null, InputArgument::REQUIRED, 'The time limit for sending messages (in seconds).')
            ->addOption('recover-timeout', null, InputArgument::REQUIRED, 'The timeout for recovering messages that have taken too long to send (in seconds).');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $mailer = $this->getMailer();

        $transport = $mailer->getTransport();
        if ($transport instanceof \Swift_Transport_SpoolTransport) {
            $spool = $transport->getSpool();
            if ($spool instanceof \Swift_ConfigurableSpool) {
                $spool->setMessageLimit((int) $input->getOption('message-limit'));
                $spool->setTimeLimit((int) $input->getOption('time-limit'));
            }
            if ($spool instanceof \Swift_FileSpool) {
                if (null !== $recoverTimeout) {
                    $spool->recover((int) $input->getOption('recover-timeout'));
                } else {
                    $spool->recover();
                }
            }
            $sent = $spool->flushQueue($mailer->getRealTransport());
            $io->text(sprintf('<comment>%d</comment> emails sent', $sent));
        } else {
            $io->warning('Transport is not a Swift_Transport_SpoolTransport.');
        }
    }

    /**
     * Returns the TYPO3 mailer.
     *
     * @return \TYPO3\CMS\Core\Mail\Mailer
     */
    protected function getMailer(): Mailer
    {
        return GeneralUtility::makeInstance(Mailer::class);
    }
}
