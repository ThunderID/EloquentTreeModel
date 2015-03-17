#Attributes
```
name				: name of the node
tree_path			: path of the node
tree_path_delimiter	: delimiter of the path
```

#Usage

* Create a model class 
* Add this code to your model:
```
use \ThunderID\EloquentTreeModel\ITreeModel;
use \ThunderID\EloquentTreeModel\TreeModelTrait;
use \ThunderID\EloquentTreeModel\TreeModelObserver;
```
* Update your model to implements ITreeModel
```
class MyModel extends Model implements ITreeModel 
```
* Use TreeModel trait in your model
```
use TreeModelTrait;
```
* Assign observer to your model
```
static::observe(new TreeModelObserver);
```
