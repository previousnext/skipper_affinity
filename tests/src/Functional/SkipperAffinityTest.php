<?php

namespace Drupal\Tests\skipper_affinity\Functional;

use Drupal\simpletest\UserCreationTrait;
use Drupal\skipper_affinity\SkipperAffinityEventSubscriber;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests skipper affinity.
 *
 * @group skipper_affinity
 */
class SkipperAffinityTest extends BrowserTestBase {

  use UserCreationTrait;

  /**
   * User to test with.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $testUser;
  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'skipper_affinity',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->testUser = $this->drupalCreateUser();
  }

  /**
   * Tests skipper affinity.
   */
  public function testSkipperAffinity() {
    $this->drupalGet('<front>');
    // There should be no cookie set for a non-logged in user.
    $session = $this->getSession();
    $this->assertEmpty($session->getCookie(SkipperAffinityEventSubscriber::COOKIE_NAME));

    // Now login.
    $this->drupalLogin($this->testUser);
    $this->assertEquals(gethostname(), $session->getCookie(SkipperAffinityEventSubscriber::COOKIE_NAME));

    // And logout again.
    $this->drupalLogout();
    $this->assertEmpty($session->getCookie(SkipperAffinityEventSubscriber::COOKIE_NAME));
  }

}
