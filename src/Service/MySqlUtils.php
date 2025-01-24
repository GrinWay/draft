<?php

namespace App\Service;

use Carbon\Carbon;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\InputStream;
use Symfony\Component\Process\Process;
use Symfony\Component\String\Slugger\SluggerInterface;

// TODO: MySqlUtils to backup and restore
class MySqlUtils
{
    private readonly Filesystem $filesystem;

    public function __construct(
        private readonly SluggerInterface                                     $slugger,
        #[Autowire('%app.database.backup_abs_path%')] private readonly string $backupAbsPath,
    )
    {
        $this->filesystem = new Filesystem();
    }

    public function backup()
    {
        $filename = \sprintf(
            '%s.sql',
            $this->slugger->slug(Carbon::now('UTC')->format('T d-m-Y')),
        );
        $absFilename = $this->backupAbsPath . '/' . $filename;

        $process = Process::fromShellCommandline(\sprintf('docker exec -it app_pure_db mysqldump --user root --password --port 3306 --databases root --skip-comments > "%1$s"', $absFilename));

        $input = new InputStream();
        $input->write($password = 'root');
        $process->setInput($input);

        $process->mustRun();
        $input->close();

//        $output = $process->getOutput();
//        if (!empty($output)) {
//            $this->filesystem->dumpFile($absFilename, $output);
//        } else {
//            $notifier->notify('email');
//        }
    }
}
