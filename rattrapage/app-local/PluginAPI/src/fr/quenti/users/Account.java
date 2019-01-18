package fr.quenti.users;

/**
 * 
 * @author quenti77
 * @version 0.0.0.1-dev
 */
public class Account {
	
	private Integer id;
	private String userName;
	private String mail;
	
	public Account(Integer id, String userName, String mail) {
		this.id = id;
		this.userName = userName;
		this.mail = mail;
	}

	public Integer getId() {
		return id;
	}
	
	public String getUserName() {
		return userName;
	}

	public String getMail() {
		return mail;
	}

	@Override
	public boolean equals(Object obj) {
		if (obj == this) {
			return true;
		}
		
		if (obj instanceof Account) {
			Account test = (Account) obj;
			
			return (test.userName.equalsIgnoreCase(this.userName) &&
					test.mail.equalsIgnoreCase(this.mail));
		}
		
		return false;
	}

	@Override
	public int hashCode() {
		return this.userName.hashCode() + this.mail.hashCode();
	}

	@Override
	public String toString() {
		String result = "Account: {\n";
		result += "\tuserName : " + this.userName + "\n";
		result += "\tmail     : " + this.mail + "\n";
		result += "}";
		
		return result;
	}
}
