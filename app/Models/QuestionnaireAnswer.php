<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireAnswer extends Model
{
    protected $fillable = ['questionnaire_response_id', 'question_id', 'jawaban'];

    public function question() {
        return $this->belongsTo(Question::class);
    }
}