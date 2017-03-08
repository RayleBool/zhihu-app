<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\AnswerRepository;
use App\Http\Requests\StoreAnswerRequest;
use Auth;


class AnswersController extends Controller
{
    protected $asnwerRepository;

    public function __construct(AnswerRepository $answerRepository)
    {
        $this->asnwerRepository   = $answerRepository;
    }

    public function store(StoreAnswerRequest $request, $question)
    {
        $data       = [
            'question_id'   => $question,
            'user_id'       => Auth::id(),
            'body'          => $request->get('body'),
        ];
        $answer     = $this->asnwerRepository->create($data);

        $answer->question()->increment('answers_count');

        return back();
    }
}
