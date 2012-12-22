<?php

namespace Town\Controller;

use Zend\View\Model\ViewModel;

class BlacksmithController extends BuildingController {
	protected $buildingType = 'blacksmith';
	
	public function indexAction() {
		// Get the players recipes that can be used in the blacksmith
		$recipes = $this->getRecipeModel()->getRecipesForPlayer(1, 'blacksmith');
		
		// Get player inventory statistics
		$nrOfIronInInventory = $this->getInventoryModel()->getNumberOfSpecificItemInPlayerInventory(1, 'iron');
		
		return array(
			'recipes' => $recipes,
			'nrOfIron' => $nrOfIronInInventory
		);
	}
	
	public function forgeAction() {
		$request = $this->getRequest();
		if(!$request->isPost()) {
			// Request must be POST
			return $this->redirect()->toRoute('town', array(
				'controller' => 'blacksmith',
				'longitude' => $this->longitude,
				'latitude' => $this->latitude
			));
		}
		
		$recipeID = $request->getPost()->recipe;
		
		if(!$this->getRecipeModel()->playerHasRecipe(1, $recipeID)) {
			// TODO: Report player for fucking with request
			return $this->redirect()->toRoute('town', array(
				'controller' => 'blacksmith',
				'longitude' => $this->longitude,
				'latitude' => $this->latitude
			));
		}
		
		// Do the player have enough resources to forge the chosen recipe
		$recipe = $this->getRecipeModel()->getRecipe($recipeID);
		$nrOfIronInInventory = $this->getInventoryModel()->getNumberOfSpecificItemInPlayerInventory(1, 'iron');
		
		if($nrOfIronInInventory < $recipe->iron) {
			// TODO: Report player for fucking with request
			return $this->redirect()->toRoute('town', array(
				'controller' => 'blacksmith',
				'longitude' => $this->longitude,
				'latitude' => $this->latitude
			));
		}
		
		// Delete the amount of iron needed for the recipe from the players inventory
		for($i = 0; $i < $recipe->iron; $i++) {
			$this->getInventoryModel()->deleteItemOfNameFromPlayerInventory(1, 'iron');
		}
		
		// Give the recipe item to the player
		$this->getInventoryTable()->giveItemToPlayer($recipe->itemID, 1);
		
		$message = 'Du smidde oppskriften.';
		
		$viewModel = new ViewModel($this->indexAction());
		$viewModel->setVariable('message', $message);
		$viewModel->setTemplate('town\blacksmith\index');
		
		return $viewModel;
	}
}

?>