
public class Sentiment
{
	private String type;
	private double score;
	private int mixed;
	
	public Sentiment(String type, double score, int mixed)
	{
		this.type = type;
		this.score = score;
		this.mixed = mixed;
	}
	
	public int getMixed()
	{
		return mixed;
	}
	public double getScore()
	{
		return score;
	}
	public String getType()
	{
		return type;
	}
}
