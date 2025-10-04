package com.example.quizapp;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Button;
import android.widget.TextView;
import androidx.appcompat.app.AppCompatActivity;

public class ResultsActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_results);

        TextView textViewFinalScore = findViewById(R.id.text_view_final_score);
        Button buttonPlayAgain = findViewById(R.id.button_play_again);

        int score = getIntent().getIntExtra("finalScore", 0);
        textViewFinalScore.setText("Final Score: " + score);

        buttonPlayAgain.setOnClickListener(v -> {
            Intent intent = new Intent(ResultsActivity.this, MainActivity.class);
            startActivity(intent);
            finish();
        });
    }
}