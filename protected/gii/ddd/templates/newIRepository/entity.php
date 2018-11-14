<?php echo "<?php\n"; ?>
/**
 * This is Entity Class for <?php echo $entity; ?>.
 * Auto Generated.
 * DateTime: <?php echo date("Y-m-d H:i:s")."\n"; ?>
 * Describe：
 *
 */

namespace <?php echo $namespace; ?>;

use <?php echo $this->baseEntity; ?>;
<?php if(!empty($this->iAggregateRoot)){ echo "use ".$this->iAggregateRoot.";";  } ?>


class <?php echo $entity; ?> extends <?php echo $this->baseEntityShortName; ?> <?php if(!empty($this->iAggregateRoot)){  echo "implements ".$this->iAggregateRootShortName;} ?>

{
<?php if(!empty($this->iAggregateRoot)){ ?>
    /**
    * 获取id
    * @return int
    */
    public function getId()
    {
        return $this->id;
    }

    /**
    * 设置id
    * @param $value
    */
    public function setId($value)
    {
        $this->id = $value;
    }
<?php } ?>
}

