Feature: Forms containing upload files can be uploaded automatically after validation
    In order to normalize the way uploads are handled
    As a developer
    I want the forms to automatically move files to configred target after valid submission

    Background:
        Given I write in "App/Form/DocumentType.php":
        """
        <?php namespace App\Form {
            class DocumentType extends \Symfony\Component\Form\AbstractType
            {
                public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
                {
                    $builder->add('fileObject', 'file', array(
                        'upload_directory' => 'uploads/document',
                        'target_path' => 'filePath',
                    ));
                }
                public function setDefaultOptions(\Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver) {
                    $resolver->setDefaults(array('csrf_protection' => false));
                }
                public function getName() { return 'document'; }
            }
        }
        """
        And I write in "App/Controller/FooController.php":
        """
        <?php namespace App\Controller {
            class Document { public $filePath; public $fileObject; }
            class FooController extends \Knp\RadBundle\Controller\Controller
            {
                public function newAction() {
                    return array('form' => $this->createBoundObjectForm(new Document)->createView());
                }
                public function createAction() {
                    $form = $this->createBoundObjectForm(new Document);
                    $form->handleRequest($request);

                    die(var_dump($form->isValid(), $form->getData()->filePath));
                }
            }
        }
        """
        And I write in "App/Resources/views/Foo/new.html.twig":
        """
            <form action="{{ path('app_foo_create')}}" method="POST" {{ form_enctype(form) }}>
                {{ form_widget(form) }}
                <input type="submit" value="submit" />
            </form>
        """
        And I write in "App/Resources/config/routing.yml":
        """
        App:Foo: ~
        """

    @javascript
    Scenario: Valid form with upload
        Given I visit "app_foo_new" page
        When I attach the file "test.png" to "File object"
        Then I should see "true"
        And  I should see "uploads/document/"
