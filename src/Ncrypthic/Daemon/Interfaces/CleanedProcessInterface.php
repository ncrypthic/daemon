<?php
/*
 *  Copyright (c) 2013 WhatIWear.com
 */
namespace Ncrypthic\Daemon\Interfaces;

use Psr\Log\LoggerInterface;

/**
 * Task with exit action
 * 
 * @author Lim Afriyadi <lim.afriyadi.id@gmail.com>
 */
interface CleanedProcessInterface extends ProcessInterface
{
    /**
     * Exit action
     */
    public function onExit(LoggerInterface $logger);
}
