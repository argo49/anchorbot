import java.util.ArrayList;


public class Disambiguated
{
	private String name;
	private String website;
	private String dbpedia;
	private String freebase;
	private String yago;
	private String crunchbase;
	private ArrayList<String> sybType;
	
	public String getCrunchbase()
	{
		return crunchbase;
	}
	public String getDbpedia()
	{
		return dbpedia;
	}
	public String getFreebase()
	{
		return freebase;
	}
	public String getName()
	{
		return name;
	} 
	public ArrayList<String> getSybType()
	{
		return sybType;
	}
	public String getWebsite()
	{
		return website;
	}
	public String getYago()
	{
		return yago;
	}
}
