
public class Entity
{
	private String type;
	private double relevance;
	private Sentiment sentiment;
	private int count;
	private String text;
	private Disambiguated dis;
	
	public int getCount()
	{
		return count;
	}
	public Disambiguated getDis()
	{
		return dis;
	}
	public double getRelevance()
	{
		return relevance;
	}
	public Sentiment getSentiment()
	{
		return sentiment;
	}
	public String getText()
	{
		return text;
	}
	public String getType()
	{
		return type;
	}

}
