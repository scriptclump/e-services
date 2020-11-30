/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('gds_return_track', {
    return_track_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    initiated_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    return_track_status_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    track_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    received_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    return_reason_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    approved: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    return_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'gds_returns',
        key: 'return_id'
      }
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
    }
  }, {
    tableName: 'gds_return_track'
  });
};
