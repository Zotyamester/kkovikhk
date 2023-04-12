<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\YoungTeacher;
use App\Models\Vote;
use App\Models\YoungVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Auth;

class VoteController extends Controller
{
    // TODO IMPLEMENT BACKEND CHECKS FOR THE FORM, IF NOT NULL!!!
    public function vote()
    {
        $current_user = Auth::user();
        $already_voted = $current_user->has_already_voted();
        if ($already_voted)
            abort(403, "Már szavaztál");

        $teachers = Teacher::all();
        return view('vote', compact('teachers'));
    }

    // TODO: weighted voting + all three of the votes should be packed together in a POST request
    public function votepost(Request $request)
    {
        $current_user = Auth::user();
        if ($current_user->has_already_voted())
            abort(403, "Már szavaztál");

        $validated = $request->validate([
            'id' => 'required|array|between:1,3',
            'prio' => 'required|array|size:' . count($request->id),
            'prio.*' => 'required|numeric|distinct|between:1,3',
        ]);

        // TODO: maybe it could be just a simple a validation rule
        // first pass: see if all of the presented ids are valid and are of a teacher's
        foreach ($validated['id'] as $teacher_id) {
            if (!Teacher::find($teacher_id)) {
                return redirect('/vote');
            }
        }

        $ids_and_weights = array_map(null, $validated['id'], $validated['prio']);

        // second pass: create votes with weights
        foreach ($ids_and_weights as $id_and_weight) {
            $teacher_id = $id_and_weight[0];
            $weight = 4 - $id_and_weight[1];

            Log::debug("Weight is" . $weight);

            $new_vote = new Vote([
                'user_id' => $current_user->id,
                'teacher_id' => $teacher_id,
                'weight' => $weight
            ]);
            $new_vote->save();
        }

        return redirect('/voteselect');
    }

    public function youngvote()
    {
        $current_user = Auth::user();
        $already_voted = $current_user->has_already_voted_young();
        if ($already_voted)
            abort(403, "Már szavaztál");

        $teachers = YoungTeacher::all();
        return view('youngvote', compact('teachers'));
    }

    public function youngvotepost(Request $request)
    {
        $current_user = Auth::user();
        if ($current_user->has_already_voted_young())
            abort(403, "Már szavaztál");

        $validated = $request->validate([
            'id' => 'required',
        ]);

        $teacher_id = (int)$validated['id'];

        if (!YoungTeacher::find($teacher_id)) {
            return redirect('/youngvote');
        }

        $new_vote = new YoungVote([
            'user_id' => $current_user->id,
            'teacher_id' => $teacher_id,
        ]);
        $new_vote->save();

        return redirect('/');
    }
}
