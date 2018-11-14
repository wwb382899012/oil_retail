<?php echo "<?php\n"; ?>
/**
 * This is Entity Repository for <?php echo $entity; ?>.
 * Auto Generated.
 * DateTime: <?php echo date("Y-m-d H:i:s")."\n"; ?>
 * Describe：
 *
 */

namespace <?php echo $namespace; ?>;

<?php if(!empty($this->iAggregateRoot)){ echo "use ".$this->iAggregateRoot.";\n";  } ?>
use <?php echo $this->entityNamespace."\\".$this->entity; ?>;
use <?php echo $this->entityNamespace."\\I".$this->entity."Repository"; ?>;
use <?php echo $this->baseRepository; ?>;

class <?php echo $entity; ?>Repository extends <?php echo $this->baseRepositoryShortName; ?> implements I<?php echo $entity; ?>Repository
{
    public function getNewEntity()
    {
        return new <?php echo $entity; ?>();
    }

    public function getActiveRecordClassName()
    {
        return "<?php echo $entity; ?>";
    }
}
