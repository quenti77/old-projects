package fr.quenti.plugins;

/**
 * Permet de stocker les informations d'un plugin
 * @author quenti77
 * @version 0.0.0.1-dev
 */
public class InformationPlugin {
	
	private String name;
	private String author;
	private String version;
	
	/**
	 * Constructeur par défaut de la class InformationPlugin
	 * @param args0 : Le nom du plugin
	 * @param args1 : Le nom de l'auteur du plugin
	 * @param args2 : La version du plugin
	 */
	public InformationPlugin(String args0, String args1, String args2) {
		this.name = args0;
		this.author = args1;
		this.version = args2;
	}
	
	/* Getters */
	
	/**
	 * Retourne le nom du plugin
	 * @return String : Le nom du plugin
	 */
	public String getName() {
		return this.name;
	}
	
	/**
	 * Retourne le nom de l'auteur du plugin
	 * @return String : L'auteur du plugin
	 */
	public String getAuthor() {
		return this.author;
	}
	
	/**
	 * Retourne le numéro de version du plugin
	 * @return String : Version du plugin
	 */
	public String getVersion() {
		return this.version;
	}
	
	/**
	 * Test et retourne si l'objet tester est égale à l'objet actuel
	 * @param arg0 : L'objet à tester
	 * @return boolean : Si l'objet est identique à celui passé
	 */
	public boolean equals(Object arg0) {
		if (arg0 == this) {
			return true;
		}
		
		if (arg0 instanceof InformationPlugin) {
			InformationPlugin compare = (InformationPlugin) arg0;
			
			return ( compare.name.equalsIgnoreCase( this.name ) && 
					 compare.author.equalsIgnoreCase( this.author ) && 
					 compare.version.equalsIgnoreCase( this.version ));
		}
		
		return false;
	}
	
	/**
	 * Retourne le hashCode de la classe
	 * @return int : HashCode de la classe
	 */
	public int hashCode() {
		return ( this.name.hashCode() +
				 this.author.hashCode() +
				 this.version.hashCode() );
	}
	
	/**
	 * Retourne la chaine correspondant à la classe
	 * @return String : Chaine de débug
	 */
	public String toString() {
		String result = "InformationPlugin : {\n";
		
		result += "\tname : \t\t" + this.name + "\n";
		result += "\tauthor : \t\t" + this.author + "\n";
		result += "\tversion : \t\t" + this.version + "\n";
		
		result += "}";
		
		return result;
	}
}
