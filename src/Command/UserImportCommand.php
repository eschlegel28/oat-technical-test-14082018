<?php
/**
 * Created by PhpStorm.
 * User: eschlegel
 * Date: 14/08/2018
 * Time: 18:44
 */

namespace App\Command;

use App\Services\ConvertFileToArrayService;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use FOS\OAuthServerBundle\Entity\ClientManager;


/**
 * Class UserImportCommand
 * create command to import the user file in the db
 * @package Command
 */
class UserImportCommand extends ContainerAwareCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        // Name and description for init database app/console command
        $this
            ->setName('app:create-user')
            ->setDescription('Import users from  file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Showing when the script is launched
        $now = new \DateTime();
        $output->writeln('<comment>Start : ' . $now->format('d-m-Y G:i:s') . ' ---</comment>');

        // Importing CSV on DB via Doctrine ORM
        $this->import($input, $output);

        // Showing when the script is over
        $now = new \DateTime();
        $output->writeln('<comment>End : ' . $now->format('d-m-Y G:i:s') . ' ---</comment>');
    }

    protected function import(InputInterface $input, OutputInterface $output)
    {
        // Getting php array of data from entry file
        $data = $this->get($output);

        // Getting doctrine manager
        $em = $this->getContainer()->get('doctrine')->getManager();

        // Define the size of record, the frequency for persisting the data and the current index of records
        $size = count($data);
        $batchSize = 20;
        $i = 1;

        // Starting progress
        $progress = new ProgressBar($output, $size);
        $progress->start();

        // Processing on each row of data
        foreach ($data as $row) {

            $user = $em->getRepository('App:User')->findOneByEmail($row['email']);

            // If the user doest not exist we create one
            if (!is_object($user)) {
                $user = new User($row['email']);
            }

            // Updating info
            $user->setlogin($row['login']);
            $user->setPassword($row['password']);
            $user->setTitle($row['title']);
            $user->setLastname($row['lastname']);
            $user->setFirstname($row['firstname']);
            $user->setGender($row['gender']);
            $user->setAddress($row['address']);
            $user->setPicture($row['picture']);

            // Persisting the current user
            $em->persist($user);

            // Each 20 users persisted we update the progress bar
            if (($i % $batchSize) === 0) {
                // Advancing for progress display on console
                $progress->advance($batchSize);
                $now = new \DateTime();
                $output->writeln(' of users imported ... | ' . $now->format('d-m-Y G:i:s'));

            }
            $i++;
        }
        // Flushing and clear data on queue
        $em->flush();
        $em->clear();

        // Ending the progress bar process
        $progress->finish();
    }

    protected function get(OutputInterface $output)
    {
        // Getting the filenamesystem from parameters
        $fileName = $this->getContainer()->getParameter('import-file-path')."/".$this->getContainer()->getParameter('import-file-name').'.'.$this->getContainer()->getParameter('import-file-extension');
        $output->writeln('file used :  ' . $fileName);

        // Using symfony serializer for converting file to PHP Array
        return $this->getContainer()->get('serializer')->decode(file_get_contents($fileName), $this->getContainer()->getParameter('import-file-extension'));
    }
}