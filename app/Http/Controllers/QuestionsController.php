<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreQuestionRequest;

use Auth;
use App\Repositories\QuestionRepository;

class QuestionsController extends Controller
{
    protected $questionRepository;

    public function __construct(QuestionRepository $questionRepository)
    {
        $this->questionRepository   = $questionRepository;
        $this->middleware('auth')->except(['index', 'show']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $questions  = $this->questionRepository->getQuestionsFeed();
        return view('questions.index', compact('questions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('questions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreQuestionRequest $request)
    {
        $topics     = $this->questionRepository->normalizeTopic($request->get('topics'));
        $data   = [
            'title'     => $request->get('title'),
            'body'      => $request->get('body'),
            'user_id'   => Auth::id(),
        ];

        // $question   = Question::create($data);
        $question   = $this->questionRepository->create($data);

        $question->topics()->attach($topics);

        return redirect()->route('question.show', [$question->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $question   = Question::with('topics')->find($id);
        $question   = $this->questionRepository->byIdWithTopicsAndAnswer($id);
        return view('questions.show', compact('question'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $question   = $this->questionRepository->byId($id);
        if (Auth::user()->owns($question)) {
            return view('questions.edit', compact('question'));
        }

        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreQuestionRequest $request, $id)
    {
        //
        $question   = $this->questionRepository->byId($id);
        $topics     = $this->questionRepository->normalizeTopic($request->get('topics'));

        $question->update([
            'title'     => $request->get('title'),
            'body'      => $request->get('body'),
        ]);

        $question->topics()->sync($topics);

        return redirect()->route('question.show', [$question->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $question   = $this->questionRepository->byId($id);

        if (Auth::user()->owns($question)) {
            $question->delete();

            return redirect('/');
        }

        abort('403', 'Forbidden');//return back();
    }

    // private function normalizeTopic(array $topics)
    // {
    //     return collect($topics)->map(function ($topic) {
    //         if (is_numeric($topic)) {
    //             Topic::find($topic)->increment('questions_count');
    //             return (int)$topic;
    //         }
    //         $newTopic   = Topic::create(['name' => $topic, 'questions_count' => 1]);
    //         return $newTopic->id;
    //     })->toArray();
    // }
}
