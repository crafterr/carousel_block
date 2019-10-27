<?php
namespace Drupal\carousel_block\Form;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Messenger\MessengerInterface;


class CarouselForm extends ContentEntityForm {

  protected $messenger;

  /**
   * ProductForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface|NULL $entity_type_bundle_info
   * @param \Drupal\Component\Datetime\TimeInterface|NULL $time
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   */
  public function __construct(EntityRepositoryInterface $entity_repository, EntityTypeBundleInfoInterface $entity_type_bundle_info = NULL, TimeInterface $time = NULL, MessengerInterface $messenger) {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);
    $this->messenger = $messenger;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.repository'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time'),
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @return array
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\carousel\Entity\Carousel */
    $form = parent::buildForm($form,$form_state);
    //dump($form); die();
    return $form;
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @return int
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = &$this->entity;

    $status = parent::save($form,$form_state);
    switch ($status) {
      case SAVED_NEW:
        $this->messenger->addMessage($this->t('Created the %label Product.', [
          '%label' => $entity->label(),
        ]));
        break;
      default:
        $this->messenger->addMessage(
          $this->t('Saved the %label Product.', [
            '%label' => $entity->label(),
          ])
        );
    }

    $form_state->setRedirect('entity.carousel.collection');
  }


}