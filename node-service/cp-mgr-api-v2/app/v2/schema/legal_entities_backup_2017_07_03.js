/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('legal_entities_backup_2017_07_03', {
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    business_legal_name: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    legal_entity_type_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    business_type_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    le_code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    address1: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    address2: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    city: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    state_id: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    country: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    pincode: {
      type: DataTypes.STRING(12),
      allowNull: false
    },
    pan_number: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    tin_number: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    gstin: {
      type: DataTypes.STRING(50),
      allowNull: true,
      defaultValue: '0'
    },
    reach_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    profile_completed: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    website_url: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    logo: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    logo_thumbnail: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    parent_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    longitude: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    latitude: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    rel_manager: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    status_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    city_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    locality: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    landmark: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    is_approved: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '1'
    },
    approved_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    approved_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    is_posted: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    tally_resp: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'legal_entities_backup_2017_07_03'
  });
};
