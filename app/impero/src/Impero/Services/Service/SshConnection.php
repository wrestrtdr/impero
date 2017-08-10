<?php namespace Impero\Services\Service;

use Exception;

class SshConnection
{

    /**
     * @var resource
     */
    protected $connection;

    public function __construct($host, $user, $port, $key, $type = 'key')
    {
        /**
         * Create connection.
         */
        d('connecting to ' . $host . ' : ' . $port);
        $this->connection = ssh2_connect($host, $port);

        if (!$this->connection) {
            throw new Exception('Cannot estamblish SSH connection');
        }

        /**
         * Fingerprint check.
         */
        if ($type == 'key') {
            $keygen = null;
            $command = 'ssh-keygen -lf ' . $key . '.pub -E MD5';
            //d("command", $command);
            exec($command, $keygen);
            $keygen = $keygen[0] ?? null;
            $fingerprint = ssh2_fingerprint($this->connection, SSH2_FINGERPRINT_MD5 | SSH2_FINGERPRINT_HEX);
            $publicKeyContent = file_get_contents($key . '.pub');
            $content = explode(' ', $publicKeyContent, 3);
            $calculated = join(':', str_split(md5(base64_decode($content[1])), 2));
            //d($calculated, $keygen, $fingerprint);

            if (!strpos($keygen, $calculated) || $fingerprint != $keygen) {
                d("Wrong server fingerprint");
            }
        }

        /**
         * Authenticate with public and private key.
         */
        d('authenticating ' . $user . ' with ' . $key);

        if ($type == 'key') {
            $auth = ssh2_auth_pubkey_file($this->connection, $user, $key . '.pub', $key, '');
        } else {
            $auth = ssh2_auth_password($this->connection, $user, $key);
        }

        /**
         * Throw exception on misconfiguration.
         */
        if (!$auth) {
            throw new Exception("Cannot authenticate with key");
        }
    }

    public function exec($command, &$errorStreamContent = null)
    {
        $stream = ssh2_exec($this->connection, $command);

        $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

        stream_set_blocking($errorStream, true);
        stream_set_blocking($stream, true);

        $errorStreamContent = stream_get_contents($errorStream);

        return stream_get_contents($stream);
    }

    public function close()
    {
        if ($this->connection) {
            ssh2_exec($this->connection, 'exit');
        }

        return $this;
    }

    public function sftpSend($local, $remote, $mode = null)
    {
        $sftp = ssh2_sftp($this->connection);

        $stream = fopen("ssh2.sftp://" . intval($sftp) . $remote, 'w');

        return @fwrite($stream, file_get_contents($local));
    }

}