package com.example.quizapp;

import android.content.Intent;
import android.os.Bundle;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import java.util.ArrayList;
import java.util.List;

public class MainActivity extends AppCompatActivity {

    private RecyclerView recyclerView;
    private CategoryAdapter adapter;
    private List<Category> categoryList;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        recyclerView = findViewById(R.id.recycler_view_category);
        recyclerView.setHasFixedSize(true);
        recyclerView.setLayoutManager(new LinearLayoutManager(this));

        categoryList = new ArrayList<>();
        // In a real app, you would fetch this from a database or API
        categoryList.add(new Category(1, "Science"));
        categoryList.add(new Category(2, "History"));
        categoryList.add(new Category(3, "Math"));

        adapter = new CategoryAdapter(categoryList);
        recyclerView.setAdapter(adapter);

        adapter.setOnItemClickListener(position -> {
            Category clickedCategory = categoryList.get(position);
            Intent intent = new Intent(MainActivity.this, QuizActivity.class);
            intent.putExtra("categoryName", clickedCategory.getName());
            startActivity(intent);
        });
    }
}