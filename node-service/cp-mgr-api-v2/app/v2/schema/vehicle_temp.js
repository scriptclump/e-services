/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vehicle_temp', {
    veh_temp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    dc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    hub_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    reg_name: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    driver_name: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    mobile_no: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    kms_travelled: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    rent: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    vehicle_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    make: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    model_name: {
      type: DataTypes.STRING(50),
      allowNull: true
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
      allowNull: false
    }
  }, {
    tableName: 'vehicle_temp'
  });
};
