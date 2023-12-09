<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;



class ReviewController extends Controller
{
    public function createReview(Request $request)
    {
        if(!Auth::check())
            return response()->json(['error' => 'Unauthenticated.'], 401);
        
        $rating = $request->input('rating');
        $reviewText = $request->input('review');


        $item = Item::find($request->input('itemId'));

        $existingReview = $item->reviews()->where('id_user', Auth::id())->get();
        $userName = User::find(Auth::id())->username;

        $reviews = $item->reviews()->get();


        if($existingReview->isEmpty()){
            $entry = new Review;

            $entry->id_user = Auth::id();
            $entry->id_item = $item->id;
            $entry->rating = $rating;
            $entry->description = $reviewText;
            $entry->up_votes = 0;
            $entry->down_votes = 0;

            $entry->save();
            $newRating =  $item->reviews()->avg('rating');
            return response()->json(['rating' => $newRating, 'username' => $userName, 'reviewText' => $reviewText, 'reviewRating' => $rating]);

        }else{
            return response()->json(['error' => 'You have already reviewed this item.']);
        }   
    }

    public function deleteReview($id, Request $request)
    {
        if(!Auth::check())
            return response()->json(['error' => 'Unauthenticated.'], 401);
        
        $review = Review::find($id);
    
        if(!$review){
            return response()->json(['error' => 'Review not found.'], 404);
        }
    
        if(Auth::user()->id != $review->id_user){
            return response()->json(['error' => 'Unauthorized.'], 403);
        }
    
        $review->delete();

        $item = Item::find($review->id_item);
        $newRating =  $item->reviews()->avg('rating');


        return response()->json(['success' => true, 'newRating' => $newRating]);
    }

    public function editReview($id, Request $request){

        if(!Auth::check())
            return response()->json(['error' => 'Unauthenticated.'], 401);
        
        $rating = $request->input('rating');
        $reviewText = $request->input('description');

        $review = Review::find($id);
        $review->rating = $rating;
        $review->description = $reviewText;
        $review->save();

        $item = Item::find($review->id_item);
        $newRating =  $item->reviews()->avg('rating');

        return response()->json(['description' => $reviewText, 'rating' => $rating, 'newRating' => $newRating]);
    }
    
}

?>