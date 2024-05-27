<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{

  public function updateRecord($table, $idColumn, $id, $fields)
  {
      $builder = $this->db->table($table);
      $builder->where($idColumn, $id);

      foreach ($fields as $name => $value) {
          $builder->set($name, $value);
      }

      return $builder->update();
  }

  public function createRecord($table, $fields = [])
  {
      // Get the builder for the specified table
      $builder = $this->db->table($table);

      // Insert the fields into the table
      $builder->insert($fields);

      // Return the last inserted ID
      return $this->db->insertID();
  }

  public function updateRocord($table, $user_id, $fields)
  {
      // Update a record in the specified table
      $builder = $this->db->table($table);
      $builder->where('user_id', $user_id);
      $builder->update($fields); // Update with specified fields
  }

  public function deleteRecord($table, $conditions)
  {
      // Delete a record from the specified table based on conditions
      $builder = $this->db->table($table);
      foreach ($conditions as $key => $value) {
          $builder->where($key, $value);
      }
      $builder->delete(); // Delete the record
  }

}
