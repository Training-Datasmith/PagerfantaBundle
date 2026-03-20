<?php

declare (strict_types=1);
namespace Bab_Dev\Pagerfanta_Bundle\Event_Listener;

use Pagerfanta\Exception\Not_Valid_Max_Per_Page_Exception;
use Symfony\Component\Http_Kernel\Event\Exception_Event;
use Symfony\Component\Http_Kernel\Exception\Not_Found_Http_Exception;
final class Convert_Not_Valid_Max_Per_Page_To_Not_Found_Listener
{
    public function on_kernel_exception(Exception_Event $event): void
    {
        $throwable = $event->get_throwable();
        if ($throwable instanceof Not_Valid_Max_Per_Page_Exception) {
            $event->set_throwable(new Not_Found_Http_Exception('Page Not Found', $throwable));
        }
    }
}