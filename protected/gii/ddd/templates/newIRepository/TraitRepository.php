<?php echo "<?php\n"; ?>
/**
 * This is Entity Trait Repository for <?php echo $entity; ?>.
 * Auto Generated.
 * DateTime: <?php echo date("Y-m-d H:i:s")."\n"; ?>
 * Describe：
 *
 */

namespace <?php echo $namespace; ?>;


use ddd\Infrastructure\DIService;

trait <?php echo $entity; ?>Repository
{
    /**
    * @var I<?php echo $entity; ?>Repository
    */
    protected $<?php echo lcfirst($entity); ?>Repository;

    /**
    * 获取项目仓储
    * @return I<?php echo $entity; ?>Repository
    * @throws \Exception
    */
    protected function get<?php echo $entity; ?>Repository()
    {
        if (empty($this-><?php echo lcfirst($entity); ?>Repository))
        {
            $this-><?php echo lcfirst($entity); ?>Repository=DIService::getRepository(I<?php echo $entity; ?>Repository::class);
        }
        return $this-><?php echo lcfirst($entity); ?>Repository;
    }
}