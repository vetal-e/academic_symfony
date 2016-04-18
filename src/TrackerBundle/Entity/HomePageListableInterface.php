<?php

namespace TrackerBundle\Entity;

interface HomePageListableInterface
{
    /**
     * @return string
     */
    public function getRoute();

    /**
     * @return string
     */
    public function getCaption();
}