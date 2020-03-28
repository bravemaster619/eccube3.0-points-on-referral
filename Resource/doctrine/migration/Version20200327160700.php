<?php
namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Eccube\Common\Constant;

class Version20200327160700 extends AbstractMigration {

    protected $entities = array(
        'Plugin\PointsOnReferral\Entity\PointsOnReferralConfig',
        'Plugin\PointsOnReferral\Entity\PointsOnReferralCustomer',
        'Plugin\PointsOnReferral\Entity\PointsOnReferralHistory'
    );

    public function up(Schema $schema) {
        $app = \Eccube\Application::getInstance();
        $meta = $this->getMetadata($app['orm.em']);
        $tool = new SchemaTool($app['orm.em']);
        $tool->createSchema($meta);
    }

    public function down(Schema $schema) {
        $app = \Eccube\Application::getInstance();
        $meta = $this->getMetadata($app['orm.em']);
        $tool = new SchemaTool($app['orm.em']);
        $schemaFromMetadata = $tool->getSchemaFromMetadata($meta);

        foreach($schemaFromMetadata->getTables() as $table) {
            if ($schema->hasTable($table->getName())) {
                $schema->dropTable($table->getName());
            }
        }

        foreach ($schemaFromMetadata->getSequences() as $sequence) {
            if ($schema->hasSequence($sequence->getName())) {
                $schema->dropSequence($sequence->getName());
            }
        }
    }

    protected function getMetadata(EntityManager $em) {
        $meta = array();
        foreach ($this->entities as $entity) {
            $meta[] = $em->getMetadataFactory()->getMetadataFor($entity);
        }
        return $meta;
    }
}
