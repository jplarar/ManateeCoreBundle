<?php

namespace Manatee\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Manatee\CoreBundle\Entity\User;

class CreateAdminCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('manatee:create:user')
            ->setDescription("Create a new user for Manatee")
            ->addArgument(
                'email',
                InputArgument::REQUIRED,
                'email'
            )
            ->addArgument(
                'password',
                InputArgument::REQUIRED,
                'Password'
            )
        ;
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        # Initialize Doctrine
        $doctrine = $this->getContainer()->get('doctrine');
        /* @var \Doctrine\ORM\EntityManager $em */
        $em = $doctrine->getManager();

        // Load command arguments
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        // Load security encoder
        $factory = $this->getContainer()->get('security.encoder_factory');

        // Create user
        $user = new User();

        $user->setEmail($email);
        $user->setFullName($email);
        $user->setRole('ROLE_USER');
        $user->setIsActive(1);

        /* @var \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface $encoder */
        $encoder = $factory->getEncoder($user);
        $encodedPassword = $encoder->encodePassword($password, $user->getSalt());
        $user->setPassword($encodedPassword);

        // Persist to database
        $em->persist($user);
        $em->flush();

        $output->writeln("Created ROLE_USER user <info>" . $email . "</info> successfully!");
    }
}
