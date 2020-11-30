/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('assets_history', {
    assets_history_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    asset_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    allocation_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    allocation_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    fromdate: {
      type: DataTypes.DATE,
      allowNull: true
    },
    allocation_status: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    comment: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    isactive: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'assets_history'
  });
};
