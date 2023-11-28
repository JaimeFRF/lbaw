<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Review;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;



class ReviewController extends Controller
{
    public function createReview(Request $request)
    {
        Log::info('Creating review');
        if(!Auth::check())
            return response()->json(['error' => 'Unauthenticated.'], 401);
        
        $rating = $request->input('rating');
        $item = Item::find($request->input('itemId'));

        $existingReview = $item->reviews()->where('id_user', Auth::id())->get();

        $reviews = $item->reviews()->get();
        Log::info('Reviews: ' . $reviews);


        if($existingReview->isEmpty()){
            LOG::info('Creating new review');
            $entry = new Review;

            $entry->id_user = Auth::id();
            $entry->id_item = $item->id;
            $entry->rating = $rating;
            $entry->description = 'Test';
            $entry->up_votes = 0;
            $entry->down_votes = 0;

            $entry->save();
        }else{
            LOG::info('Updating existing review');
            $existingReview->first()->rating = $rating;
            $existingReview->first()->save();
        
        }

        $newReviews = $item->reviews()->get();
        Log::info('New Reviews: ' . $newReviews);

        return response()->json(['rating' => $rating]);
    }
}

?>