
public class EntityWrap
{
	private String name;
	private String type;
	private String sentiment;
	private String numAdjective;
	private double relevance;
	
	public EntityWrap()
	{
		// TODO Auto-generated constructor stub
	} 
	public void setName(String name)
	{
		this.name = name;
	}
	public void setNumAdjective(String numAdjective)
	{
		this.numAdjective = numAdjective;
	}
	public void setSentiment(String sentiment)
	{
		this.sentiment = sentiment;
	}
	public void setType(String type)
	{
		this.type = type;
	}
	public void setRelevance(double relevance)
	{
		this.relevance = relevance;
	}
}
