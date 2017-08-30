mespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Account;
use App\Models\Savingsaccount;
use App\Models\Savingposting;
use App\Models\Charge;
/**
 *
 */
class SavingProduct extends Model
{

  protected $table = "x_savingproducts";

  public function savingproductcoa(){

		return $this->hasMany('Account');
	}


	public function savingaccounts(){

		return $this->hasMany('Savingsaccount');
	}


	public function savingpostings(){

		return $this->hasMany('Savingposting');
	}


	public function charges(){

		return $this->belongsToMany('Charge');
	}

}



 ?>

