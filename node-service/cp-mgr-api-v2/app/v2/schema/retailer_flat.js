/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('retailer_flat', {
    retailer_flat_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    le_code: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    parent_le_id: {
      type: DataTypes.INTEGER(1),
      allowNull: false
    },
    business_legal_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    legal_entity_type_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    legal_entity_type: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    business_type_id: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    business_type: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    mobile_no: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    volume_class_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    volume_class: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    No_of_shutters: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    suppliers: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    business_start_time: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    business_end_time: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    address: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    address1: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    address2: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    area_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    area: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    hub_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    beat_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    spoke_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    beat: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    city: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    state_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    state: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    country: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    locality: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    landmark: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    pincode: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    smartphone: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    network: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    is_approved: {
      type: DataTypes.STRING(255),
      allowNull: true,
      defaultValue: 'Yes'
    },
    master_manf: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    profile_completed: {
      type: DataTypes.STRING(255),
      allowNull: true,
      defaultValue: 'Yes'
    },
    is_active: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '1'
    },
    orders_old: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    last_order_date_old: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    beat_rm_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    rank_pct: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    created_by: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    created_time: {
      type: DataTypes.TIME,
      allowNull: true
    },
    updated_by: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_time: {
      type: DataTypes.TIME,
      allowNull: true
    },
    latitude: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    longitude: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    aadhar_id: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    dist_not_serv: {
      type: DataTypes.STRING(100),
      allowNull: false,
      defaultValue: '0'
    },
    is_icecream: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      defaultValue: '0'
    },
    is_visicooler: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      defaultValue: '0'
    },
    is_milk: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      defaultValue: '0'
    },
    is_deepfreezer: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      defaultValue: '0'
    },
    is_fridge: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      defaultValue: '0'
    },
    is_vegetables: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      defaultValue: '0'
    },
    facilities: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      defaultValue: '0'
    },
    is_swipe: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      defaultValue: '0'
    }
  }, {
    tableName: 'retailer_flat'
  });
};
