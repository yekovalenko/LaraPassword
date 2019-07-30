<?php
/**
 * This file is part of LaraPassword.
 * Copyright (c) 2019  Yevhenii Kovalenko
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yekovalenko\LaraPassword\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Encryption\Encrypter;

class HashCommand extends Command
{

    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larapassword:hash
                    {--show : Display the hash instead of modifying files}
                    {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the LaraPassword hash';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $hash = $this->generateRandomHash();

        if ($this->option('show')) {
            return $this->line('<comment>' . $hash . '</comment>');
        }

        if (!$this->setHashInEnvFile($hash)) {
            return;
        }

        $this->laravel['config']['larapassword.hash'] = $hash;

        $this->info("LaraPassword hash [$hash] set successfully.");
    }

    /**
     * Generate a random hash for the LaraPassword.
     *
     * @return string
     */
    protected function generateRandomHash()
    {
        return 'base64:' . base64_encode(Encrypter::generateKey($this->laravel['config']['app.cipher']));
    }

    /**
     * Set the package hash in the environment file.
     *
     * @param  string $hash
     * @return bool
     */
    protected function setHashInEnvFile($hash)
    {
        $currentHash = $this->laravel['config']['larapassword.hash'];

        if (strlen($currentHash) !== 0 && (!$this->confirmToProceed())) {
            return false;
        }

        $this->writeNewEnvironmentFileWith($hash);

        return true;
    }

    /**
     * Write a new environment file with the given hash.
     *
     * @param  string $hash
     * @return void
     */
    protected function writeNewEnvironmentFileWith($hash)
    {
        file_put_contents($this->laravel->environmentFilePath(), preg_replace(
            $this->hashReplacementPattern(),
            'LARAPASSWORD_HASH=' . $hash,
            file_get_contents($this->laravel->environmentFilePath())
        ));
    }

    /**
     * Get a regex pattern that will match env LARAPASSWORD_HASH with any random hash.
     *
     * @return string
     */
    protected function hashReplacementPattern()
    {
        $escaped = preg_quote('=' . $this->laravel['config']['larapassword.hash'], '/');

        return "/^LARAPASSWORD_HASH{$escaped}/m";
    }

}
