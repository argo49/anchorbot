import java.util.ArrayList;

import com.google.gson.Gson;
import com.google.gson.JsonArray;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;


public class txtJSONParser {

	private Gson gson;
	private JsonParser parser;
	private JsonObject obj;
	private String path;
	
	public txtJSONParser(String path)
	{
		this.path = path;
		gson = new Gson();
		parser = new JsonParser();
		obj = (JsonObject)parser.parse(path);				
	}
	
	public Gson getGson() {
		return gson;
	}
	public JsonObject getObj() {
		return obj;
	}
	public JsonParser getParser() {
		return parser;
	}
	public String getPath() {
		return path;
	}
	public String getURL() {
		return obj.get("path").toString(); 	
	}
	public ArrayList<String> getParagraphs() {
		ArrayList<String> paras = new ArrayList<String>();
		JsonArray jArray = obj.get("paragraphs").getAsJsonArray(); 
		if (jArray != null) { 
		   for (int i=0;i<jArray.size();i++){ 
		    paras.add(jArray.get(i).toString());
		   } 
		}
		return paras;	
	}
}
