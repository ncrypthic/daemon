<?php
/*
 *  Copyright (C) 2013  Lim Afriyadi <lim.afriyadi.id@gmail.com>
 * 
 *  Permission is hereby granted, free of charge, to any person obtaining a copy of
 *  this software and associated documentation files (the "Software"), to deal in 
 *  the Software without restriction, including without limitation the rights to 
 *  use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 *  the Software, and to permit persons to whom the Software is furnished to do so,
 *  subject to the following conditions:
 * 
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 * 
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 *  FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR 
 *  COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER 
 *  IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN 
 *  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace NcrypthicDaemon\Manager;

use NcrypthicDaemon\Process\ProcessInterface;
use NcrypthicDaemon\Exception as Exc;

/**
 * Default implementation of ManagerInterface
 *
 * created : Oct 8, 2013 4:43:06 PM
 * @author Lim Afriyadi <lim.afriyadi.id@gmail.com>
 */
class DefaultManager implements ManagerInterface
{
    private $index     = 0;
    private $childs    = array();
    private $processes = array();

    /**
     * {@inheritdoc}
     */
    public function addProcess( ProcessInterface $proc )
    {
        $this->processes[$this->index++] = $proc;
    }
    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->processes[$this->index];
    }
    /**
     * {@inheritdoc}
     */
    public function daemonize()
    {
        if($this->valid())
        {
            foreach($this->processes as $process)
            {
                $this->fork($process);
            }
            $this->monitor();
        }
    }
    /**
     * Find id of a process 
     * 
     * @param \NcrypthicDaemon\Process\ProcessInterface $proc
     * @return \ProcessInterface
     * @throws Exc\ProcessNotFoundException
     */
    public function findProcess( ProcessInterface $proc )
    {
        $index = array_search($proc, $this->processes, true);
        if(false === $index)
            throw new Exc\ProcessNotFoundException();
        return $index;
    }
    /**
     * Fork process
     * 
     * @throws Exc\ExtensionNotInstalledException
     * @throws Exc\ForkErrorException
     */
    public function fork(  ProcessInterface $proc)
    {
        if(!function_exists('pcntl_fork'))
            throw new Exc\ExtensionNotInstalledException();

        $pid = pcntl_fork();
        switch($pid)
        {
            case -1:
                throw new Exc\ForkErrorException();
                break;
            case 0:
//                while(true)
                    $proc->execute();
                break;
            default:
                $this->childs[$pid] = $proc;
        }
    }
    /**
     * Get a process by its id
     * 
     * @param int $index
     * @return \ProcessInterface
     * @throws Exc\ProcessNotFoundException
     */
    public function getProcess( $index )
    {
        if(isset($this->processes[$index])) 
           return $this->processes[$index];
        throw new Exc\ProcessNotFoundException();
    }
    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->index;
    }
    /**
     * Child process monitoring method
     * 
     * @throws Exc\ChildProcessException
     */
    public function monitor()
    {
        $error = false;
        while(!$error)
        {
            $pid = pcntl_wait($status, WNOHANG|WUNTRACED);
            switch($pid)
            {
                case -1:
                    $error   = true;
                    $message = pcntl_strerror(pcntl_get_last_error());
                    throw new Exc\ChildProcessException($message);
                case 0:
                    break;
                default:
                    $process = $this->childs[$pid];
                    unset($this->childs[$pid]);
                    $this->fork($process);
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    public function next()
    {
        return $this->getProcess($this->index++);
    }
    /**
     * {@inheritdoc}
     */
    public function removeProcess( ProcessInterface $proc )
    {
        $index = $this->findProcess($proc);
        unset($this->processes[$index]);
        $this->processes = array_values($this->processes);
    }
    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->index = 0;
    }
    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return !empty($this->processes);
    }    
}
