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
        /** @var Request $request */
        $request = $event->getRequest();

        //recogemos el valor del formulario de idioma
        $locale = $request->get('idiomaVal');
        
        //comprobamos si se ha cambiado el idioma, sino lo obtenemos de la session y si is empty, default
        $locale = $request->get('idiomaVal', $request->getSession()->get('_locale', $this->defaultLocale));
        
        //establecemos los valores en la session y el request
        $request->getSession()->set('_locale', $locale);
        $request->setLocale($locale);
      
    }

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 20)),
        );
    }
}