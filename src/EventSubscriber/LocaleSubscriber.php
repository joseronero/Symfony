<?php
/// src/EventSubscriber/LocaleSubscriber.php
namespace App\EventSubscriber;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LocaleSubscriber implements EventSubscriberInterface
{
    private $defaultLocale;

    public function __construct($defaultLocale = 'es')
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
            
        $locale = $request->query->get ("idiomaVal");
        //    $request->getSession ()->set ( '_locale' , $locale );
        //    $request->setLocale ( $locale );
            printf($locale);
        
    }

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 20)),
        );
    }
}