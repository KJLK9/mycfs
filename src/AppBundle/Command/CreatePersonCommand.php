<?php
/**
 * Created by PhpStorm.
 * User: KJ
 * Date: 4-5-2017
 * Time: 22:40
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\Person;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;


class CreatePersonCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('app-test');

        $this->setDescription('Creates a new user.');

        $this->setHelp('This command allows you to create a user...');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $arechten = $em->getRepository('AppBundle:rechten')->findAll();
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'User Creator',
            '============',
            '',
        ]);

        $helper = $this->getHelper('question');

        // outputs a message followed by a "\n"
        $name = new Question('Please enter the name for this person ');
        $naam = $helper->ask($input, $output, $name);

        $emailaddress = new Question('Please enter the email address for this person ');
        $email = $helper->ask($input, $output, $emailaddress);

        $password = new Question('Please enter the password for this person ');
        $password->setHidden(true);
        $password->setHiddenFallback(false);
        $pw = $helper->ask($input, $output, $password);

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Please select the role for this person ',
            $arechten,
            0
        );
        $question->setErrorMessage('Role %s is invalid.');
        $rechten = $helper->ask($input, $output, $question);

        $output->writeln('Username: '.$naam);
        $output->writeln('Email Address: '.$email);
        $output->writeln('Password: '.$pw);
        $output->writeln('Role: '.$rechten);

        $pw = password_hash($pw, PASSWORD_BCRYPT);
        $rechten = $em->getReference('AppBundle:rechten', $rechten);

        $person = new Person($naam, $email, $pw, $rechten);
        $em->persist($person);
        $em->flush($person);
        if($person){
            $output->writeln('User successfully generated!');
        }else{
            $output->writeln('Something went wrong. Retry please!');
        }

    }

}