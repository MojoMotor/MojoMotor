<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MojoMotor - by EllisLab
 *
 * @package		MojoMotor
 * @author		MojoMotor Dev Team
 * @copyright	Copyright (c) 2003 - 2012, EllisLab, Inc.
 * @license		http://mojomotor.com/user_guide/license.html
 * @link		http://mojomotor.com
 * @since		Version 1.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Member Model
 *
 * @package		MojoMotor
 * @subpackage	Models
 * @author		EllisLab Dev Team
 * @link		http://mojomotor.com
 */
class Member_model extends CI_Model {

	/**
	 * Get Member Group Name
	 *
	 * Gets a member group name
	 *
	 * @param	int
	 * @return	mixed (string on success, FALSE on fail)
	 */
	public function get_group_name($group_id = '')
	{
		$this->db->select('group_title');
		$this->db->where('id', $group_id);
		$group = $this->db->get('member_groups');

		return ($group->num_rows == 1) ? $group->row('group_title') : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Member Groups
	 *
	 * Gets member groups
	 *
	 * @return	array
	 */
	public function get_member_groups()
	{
		$groups = array();

		foreach ($this->db->get('member_groups')->result() as $group)
		{
			$groups[$group->id] = $group->group_title;
		}

		return $groups;
	}

	// --------------------------------------------------------------------

	/**
	 * Insert Member Group
	 *
	 * Inserts a new member group
	 *
	 * @param	string
	 * @return	mixed (int on success, FALSE on fail)
	 */
	public function insert_member_group($group_title = '')
	{
		$this->db->set('group_title', $group_title);

		return ($this->db->insert('member_groups')) ? $this->db->insert_id() : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Members
	 *
	 * Returns all members, accepts offset for pagination
	 *
	 * @param	mixed
	 * @param	int
	 * @param	bool
	 * @return	object
	 */
	public function get_members($limit = 10, $offset = 0, $include_password = FALSE)
	{
		$fields = 'members.id, members.email, members.password, members.group_id, member_groups.group_title';

		// $include_password is almost always not wanted, as it would be encrypted anyhow,
		// however if we are exporting member data (utilities) then we want it included.
		if ($include_password === TRUE)
		{
			$fields .= ', members.password';
		}

		// This function is also used when exporting. Hence we need all members, and '*'
		// simply signifies to grab everyone. Usually, there's a limit and offset.
		if ($limit != '*')
		{
			$this->db->limit($limit, $offset);
		}

		$this->db->select($fields);
		$this->db->join('member_groups', 'members.group_id = member_groups.id');
		return $this->db->get('members');
	}

	// --------------------------------------------------------------------

	/**
	 * Count All Members
	 *
	 * Returns a count of all members
	 *
	 * @return	int
	 */
	public function count_all_members()
	{
		return $this->db->count_all('members');
	}

	// --------------------------------------------------------------------

	/**
	 * Get Member
	 *
	 * Returns all information about any one member based on email
	 *
	 * @param	string
	 * @return	mixed
	 */
	public function get_member($email = '')
	{
		return $this->_get_member($email);
	}

	// --------------------------------------------------------------------

	/**
	 * Get Member Password Reset
	 *
	 * Returns member information for password reset
	 *
	 * @param	int
	 * @param	string
	 * @return	mixed
	 */
	public function get_member_password_reset($id = '', $passkey = '')
	{

		$this->db->where('id', $id);
		$this->db->where('auth_code', $passkey);

		$member = $this->db->get('members');

		if ($member->num_rows() == 1)
		{
			return $member;
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Get Member By Id
	 *
	 * Returns all information about any one member based on user id
	 *
	 * @param	int
	 * @return	mixed
	 */
	public function get_member_by_id($id = '')
	{
		return $this->_get_member($id, 'id');
	}

	// --------------------------------------------------------------------

	/**
	 * Get Member
	 *
	 * Returns all information about any one member
	 *
	 * @param	string
	 * @param	string
	 * @return	mixed
	 */
	protected function _get_member($needle = '', $haystack = 'email')
	{
		$this->db->where($haystack, $needle);

		$member = $this->db->get('members');
		
		return ($member->num_rows() == 1) ? $member : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Insert Member
	 *
	 * Inserts a member
	 *
	 * @param	array
	 * @return	mixed	(INT on success, BOOL on fail)
	 */
	public function insert_member($member_data = array())
	{
		if (isset($member_data['password']))
		{
			$member_data['password'] = $this->generate_password($member_data['password']);
		}

		return ($this->db->insert('members', $member_data)) ? $this->db->insert_id() : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update Member
	 *
	 * Updates a member
	 *
	 * @param	array
	 * @return	bool
	 */
	public function update_member($member_data = array())
	{
		$this->db->where('id', $member_data['id']);

		if (isset($member_data['password']))
		{
			$member_data['password'] = $this->generate_password($member_data['password']);
		}

		// affected_rows() can't be used for true/false, since a valid submission
		// that changes nothing would report as FALSE
		// return ($this->db->affected_rows() > 0) ? TRUE : FALSE;

		return ( ! $this->db->update('members', $member_data)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Delete Member
	 *
	 * Deletes a member
	 *
	 * @param	integer
	 * @return	bool
	 */
	public function delete_member($user_id = '')
	{
		$this->db->where('id', $user_id);

		$this->db->delete('members');

		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Generate Password
	 *
	 * @param	string	password
	 * @return	string
	 */
	public function generate_password($password = '')
	{
		$this->load->helper('security');

		if ($password == '')
		{
			// just something. Not secure, but neither is a blank password, and it
			// should never get passed blank anyhow.
			$password = rand();
		}

		return do_hash($password);
	}
}


/* End of file member_model.php */
/* Location: system/mojomotor/models/member_model.php */