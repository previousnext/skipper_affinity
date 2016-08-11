<?php

namespace Drupal\skipper_affinity;

use Drupal\Core\Session\AccountInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * An event subscriber to set the skipper affinity cookie for logged in users.
 */
class SkipperAffinityEventSubscriber implements EventSubscriberInterface {

  /**
   * Cookie name.
   */
  const COOKIE_NAME = 'SKIPPER_AFFINITY';

  /**
   * Current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructs a BanMiddleware object.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   */
  public function __construct(AccountInterface $current_user) {
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public function onKernelRequestSetAffinityCookie(GetResponseEvent $event) {
    $request = $event->getRequest();
    if ($this->currentUser->isAuthenticated() && $request->cookies->get(static::COOKIE_NAME) !== gethostname()) {
      // Set the cookie to the hostname.
      setcookie(static::COOKIE_NAME, gethostname(), time() + (365 * 24 * 60 * 60), base_path());
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = 'onKernelRequestSetAffinityCookie';
    return $events;
  }

}
