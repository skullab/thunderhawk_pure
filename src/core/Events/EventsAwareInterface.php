<?php

namespace Thunderhawk\Events;
use Thunderhawk\Events\Manager\ManagerInterface;
interface EventsAwareInterface {
	public function setEventsManager (ManagerInterface $eventsManager);
	public function getEventsManager ();
}