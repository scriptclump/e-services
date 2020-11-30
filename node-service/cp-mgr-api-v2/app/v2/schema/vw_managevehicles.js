/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_managevehicles', {
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    driver_le_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    parent_le_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    vehicle_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    logo: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    user_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    le_code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    contact: {
      type: DataTypes.STRING(51),
      allowNull: true
    },
    rel_manager_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_active: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    },
    reg_no: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    veh_provider: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    rel_manager: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Documents: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    created_by: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    approvedby: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Approvedon: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    status: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'vw_managevehicles'
  });
};
