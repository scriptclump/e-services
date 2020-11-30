/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_managehrproviders', {
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    parent_le_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    logo: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    supplier_name: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    user_name: {
      type: DataTypes.STRING(255),
      allowNull: false
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
      type: DataTypes.STRING(51),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    approvedby: {
      type: DataTypes.STRING(51),
      allowNull: true
    },
    Approvedon: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    status: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'vw_managehrproviders'
  });
};
