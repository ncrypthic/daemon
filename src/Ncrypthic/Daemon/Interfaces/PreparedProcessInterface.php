<?php
/*
 *  Copyright (c) 2013 WhatIWear.com
 */
namespace Ncrypthic\Daemon\Interfaces;

/**
 * Prepared task with initilization and exit actions
 * 
 * @author Lim Afriyadi <lim.afriyadi.id@gmail.com>
 */
interface PreparedProcessInterface extends CleanedProcessInterface,
                                           InitializedProcessInterface
{
}
