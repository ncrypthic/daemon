<?php
/*
 *  Copyright (c) 2013 WhatIWear.com
 */
namespace Ncrypthic\Daemon\Interfaces;

use Psr\Log\LoggerInterface;

/**
 * Task with initialization
 * 
 * @author Lim Afriyadi <lim.afriyadi.id@gmail.com>
 */
interface InitializedProcessInterface extends ProcessInterface
{
    /**
     * Initialize task before run
     */
    public function onBeforeStart(LoggerInterface $logger);
}
