export type Quiz = {
  title: string;
  intro: string;
  weights: Record<string, number>;
  questions: { id: string; q: string; options: { id: string; label: string }[] }[];
  profiles: Record<string, Record<string, Record<string, number>>>;
};

export function scoreQuiz(quiz: Quiz, answers: Record<string, string>) {
  return Object.entries(quiz.profiles).map(([slug, profile]) => ({
    slug,
    score: Object.entries(quiz.weights).reduce((score, [question, weight]) => (
      score + (profile[question]?.[answers[question]] ?? 0) * weight
    ), 0),
  })).sort((a, b) => b.score - a.score);
}
