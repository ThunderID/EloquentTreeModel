<?php namespace ThunderID\EloquentTreeModel;

interface ITreeModel extends IModel {
	
	// ----------------------------------- RELATIONSHIP --------------------------------
	/**
	 * Parent relationship rule
	 *
	 * @return void
	 **/
	public function parent();

	/**
	 * Children relationship rule
	 *
	 * @return void
	 **/
	public function children();


	// ----------------------------------- MUTATOR --------------------------------
	/**
	 * Set the tree path
	 * @param string $path
	 * @return void
	 **/
	public function setTreePathAttribute($value);

	/**
	 * Set the name
	 * @param string $path
	 * @return void
	 **/
	public function setNameAttribute($name);


	// ----------------------------------- ACCESSOR --------------------------------
	/**
	 * get ascendant models
	 *
	 * @return void
	 **/
	public function getAscendantsAttribute();

	/**
	 * get descendant models
	 *
	 * @return Models
	 **/
	public function getDescendantsAttribute();

	// ----------------------------------- SCOPE --------------------------------
	/**
	 * create query finding exact tree path
	 *
	 * @return void
	 **/
	public function scopeTreePathIs($query, $value = null);

	/**
	 * create query for path not starting with value
	 *
	 * @return void
	 **/
	public function scopeTreePathNotContains($query, $value = null);

	/**
	 * create query finding model which tree path starts with
	 *
	 * @return QueryBuilder
	 **/
	public function scopeTreePathStartWith($query, $value = null);

	/**
	 * search name
	 *
	 * @return void
	 **/
	public function scopeSearchName($query, $value = null);



	// ----------------------------------- FUNCTIONS --------------------------------

}