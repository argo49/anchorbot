import com.google.gson.Gson;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;


public class AlchemyJSONParser
{
	private Gson gson;
	private JsonParser parser;
	private JsonObject obj;
	private String path;
	
	public AlchemyJSONParser(String path)
	{
		this.path = path;
		gson = new Gson();
		parser = new JsonParser();
		obj = (JsonObject)parser.parse(path);
		
		
	}
	
	
}
