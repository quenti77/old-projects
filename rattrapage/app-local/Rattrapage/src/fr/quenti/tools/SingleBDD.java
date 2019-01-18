package fr.quenti.tools;

import java.sql.DriverManager;
import java.sql.SQLException;

import com.mysql.jdbc.Connection;

/**
 * Classe singleton pour l'accès à la bdd
 * @author quenti77
 * @version 0.0.0.1-dev
 */
public class SingleBDD {
	
	private String url = "jdbc:mysql://quenti77.fr:3306/rattrapage";
	private String user = "quentin";
	private String pass = "test";
	
	private Connection connection = null;
	
	private SingleBDD() {}
	
	public Connection getBDD() throws Exception {
		
		if (this.connection == null) {
			try {
				Class.forName("com.mysql.jdbc.Driver");
			} catch (ClassNotFoundException e) {
				throw new Exception("Mysql n'est pas présent dans l'application !\n" + e.getMessage());
			}
			
			try {
				this.connection = (Connection) DriverManager.getConnection(this.url, this.user, this.pass);
			} catch (ClassCastException e) {
				throw new Exception("Impossible de convertir la connection récupéré !\n" + e.getMessage());
			} catch (SQLException e) {
				throw new Exception("Impossible de se connecter !\n" + e.getMessage());
			}
			
		}
		
		return this.connection;
	}

	private static SingleBDD INSTANCE = null;
	
	public static Connection getInstance() throws Exception {
		if (INSTANCE == null) {
			INSTANCE = new SingleBDD();
		}
		
		return INSTANCE.getBDD();
	}
	
}

